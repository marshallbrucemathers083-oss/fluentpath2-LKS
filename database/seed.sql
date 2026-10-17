-- FluentPath Starter Content and Seed Data
-- Idempotent inserts with INSERT IGNORE or explicit checks

-- 1. Insert Initial Achievements
INSERT IGNORE INTO `achievements` (`id`, `code`, `title`, `description`, `icon`, `xp_reward`) VALUES
(1, 'first_lesson', 'Langkah Pertama', 'Selesaikan pelajaran bahasa Inggris pertama Anda.', 'award', 50),
(2, 'first_challenge', 'Tantangan Diterima', 'Selesaikan dan menangkan tantangan kuis pertama Anda.', 'zap', 30),
(3, 'five_lessons', 'Pelajar Konsisten', 'Selesaikan 5 pelajaran dari berbagai tingkatan CEFR.', 'book-open', 100),
(4, 'seven_day_streak', 'Pejuang Mingguan', 'Pertahankan rentetan belajar (streak) selama 7 hari berturut-turut.', 'flame', 150),
(5, 'first_exam', 'Ujian Perdana', 'Selesaikan simulasi tes objektif TOEFL di Exam Lab.', 'clipboard-check', 120),
(6, 'accuracy_master', 'Presisi Unggul', 'Raih akurasi 100% pada sesi latihan Academy.', 'target', 80);

-- 2. Insert Starter Sample Users (Password is 'password123')
INSERT IGNORE INTO `users` (`id`, `username`, `password_hash`, `created_at`) VALUES
(1, 'budi_santoso', '$2y$12$lwgjI6ta16/J.NNPwar2T.uZAO4ac/aYXJ21fK5VPRdigdMjmMfGS', NOW() - INTERVAL 14 DAY),
(2, 'siti_aminah', '$2y$12$lwgjI6ta16/J.NNPwar2T.uZAO4ac/aYXJ21fK5VPRdigdMjmMfGS', NOW() - INTERVAL 10 DAY),
(3, 'kevin_wijaya', '$2y$12$lwgjI6ta16/J.NNPwar2T.uZAO4ac/aYXJ21fK5VPRdigdMjmMfGS', NOW() - INTERVAL 5 DAY),
(4, 'dian_lestari', '$2y$12$lwgjI6ta16/J.NNPwar2T.uZAO4ac/aYXJ21fK5VPRdigdMjmMfGS', NOW() - INTERVAL 2 DAY);

-- 3. Insert Lessons across all 6 CEFR Levels (A1 - C2, 2 per level)
INSERT IGNORE INTO `lessons` (`id`, `level`, `skill`, `order_num`, `title`, `learning_objectives`, `explanation`, `examples`, `notes`, `practice_guide`, `further_reading`, `xp_reward`) VALUES
-- A1 - Lesson 1
(1, 'A1', 'Grammar', 1, 'Present Simple: To Be & Daily Routines',
'Understand subject pronouns, conjugate the verb "to be", and describe everyday activities using basic present simple affirmative and negative structures.',
'The Present Simple tense is used to talk about general truths, repeated actions, and daily routines. With the verb "to be" (am, is, are), we describe states, identity, nationality, and qualities.\n\nFormulas:\n- Affirmative: Subject + Verb (base form / -s/-es for he/she/it).\n- Negative: Subject + do/does not + Verb base form.\n- Question: Do/Does + Subject + Verb base form?\n\nFor the verb "to be":\n- I am, He/She/It is, You/We/They are.',
'1. Affirmative: "I wake up at 6:00 AM every day."\n2. Negative: "She does not drink coffee in the afternoon."\n3. To Be: "David is an architect and they are students."\n4. Routine: "They take the bus to the university on weekdays."',
'Remember the 3rd person singular rule: Add "-s" or "-es" when the subject is he, she, or it (e.g., he works, she watches).',
'Try writing 3 sentences about your morning routine in English before proceeding to the interactive challenge.',
'Reference: British Council LearnEnglish - Present Simple and Cambridge Grammar in Use (Basic).', 25),

-- A1 - Lesson 2
(2, 'A1', 'Vocabulary', 2, 'Everyday Objects & Personal Introductions',
'Master essential vocabulary for common personal items, professions, and polite greetings for introductions.',
'When introducing yourself, you combine personal pronouns, names, origins, and your profession. In everyday life, knowing the names of immediate objects (laptop, notebook, keys, bag) helps you navigate conversations effectively.\n\nKey phrases:\n- "Hello, my name is..."\n- "Nice to meet you."\n- "I am from Jakarta, Indonesia."\n- "I work as a software engineer."',
'1. "Excuse me, where is my notebook?"\n2. "Hello! My name is Sarah and I am a graphic designer."\n3. "These are my car keys on the kitchen table."\n4. "It is a pleasure to meet you, Mr. Pratama."',
'Use "a" before consonant sounds ("a designer") and "an" before vowel sounds ("an engineer").',
'Practice saying your name, job, and city out loud clearly.',
'Reference: Cambridge English A1 Key Vocabulary List.', 25),

-- A2 - Lesson 3
(3, 'A2', 'Grammar', 1, 'Past Simple: Regular & Irregular Verbs in Storytelling',
'Describe past events, identify common irregular verbs, and structure past simple negatives and interrogatives.',
'We use the Past Simple to talk about finished actions that happened at a specific time in the past. Time markers like yesterday, last night, two days ago, and in 2021 are common indicators.\n\nRegular verbs add "-ed" (walk -> walked, play -> played).\nIrregular verbs change their vowel or form completely (go -> went, see -> saw, buy -> bought, write -> wrote).\n\nIn negative sentences and questions, use the auxiliary verb "did" + base verb (e.g., "I did not see him", NOT "I did not saw him").',
'1. Regular: "We visited the national museum yesterday."\n2. Irregular: "She wrote an inspiring email to the team last night."\n3. Negative: "They did not finish the project on time."\n4. Question: "Did you travel to Bali during your holiday?"',
'Always revert to the base verb after "did" or "didn\'t". "Did you go?" is correct; "Did you went?" is incorrect.',
'List five activities you completed yesterday and conjugate the verbs accurately.',
'Reference: BBC Learning English - Basic Past Tense Guide.', 30),

-- A2 - Lesson 4
(4, 'A2', 'Reading', 2, 'Navigating Short Narratives & Timelines',
'Skim short informational texts, locate chronological sequence markers, and extract key details.',
'Reading effectively at the A2 level requires identifying chronological connectors: first, then, after that, next, and finally. These linkers guide you through the sequence of events without getting overwhelmed by unfamiliar descriptive adjectives.',
'Passage extract:\n"First, Maya registered for the morning seminar. Then, she met with the faculty supervisor to discuss her research proposal. After that, they reviewed the laboratory schedule together. Finally, she submitted her completed syllabus at noon."',
'Look for transition words at the start of sentences to trace the timeline of events.',
'Underline time markers while reading to quickly answer comprehension questions.',
'Reference: British Council LearnEnglish A2 Reading Skills.', 30),

-- B1 - Lesson 5
(5, 'B1', 'Grammar', 1, 'Present Perfect vs. Past Simple',
'Differentiate between unfinished life experiences without specific time (Present Perfect) and completed events at defined past times (Past Simple).',
'The Present Perfect connects the past with the present. It indicates that an action happened at an unspecified time before now, or has present relevance.\n\nFormula: Subject + have/has + past participle (V3).\n- Present Perfect keywords: already, yet, ever, never, since, for, recently.\n- Past Simple keywords: yesterday, ago, in 1999, when I was young.\n\nContrast:\n- "I have visited Tokyo." (Experience at any time in my life).\n- "I visited Tokyo in 2019." (Specific, completed past moment).',
'1. "Have you ever tried traditional Rendang?"\n2. "I have worked at this technology company for three years." (Still working here).\n3. "She left the office twenty minutes ago." (Specific past time).\n4. "We haven\'t received the revised quotation yet."',
'Never use specific past time markers (yesterday, last month) with the Present Perfect.',
'Formulate two sentences about your life achievements using "have never" and "have already".',
'Reference: Cambridge English Grammar in Use (Intermediate).', 35),

-- B1 - Lesson 6
(6, 'B1', 'Listening', 2, 'Decoding Spoken Directions & Contextual Cues',
'Identify directional prepositions, listen for tone emphasis, and understand conversational exchanges in workplace contexts.',
'When listening to spoken English, speakers frequently connect words together (connected speech). Pay attention to stressed content words (verbs, nouns, negative contractions) rather than trying to hear every unstressed preposition.',
'Spoken dialogue transcript:\n"Officer: Go straight past the reception desk, turn left at the corridor, and take the second door on your right.\nVisitor: Is it near the conference hall?\nOfficer: Yes, directly opposite the cafeteria."',
'Focus on landmark words (reception, corridor, opposite) when listening to directions.',
'Replay the synthesized audio prompt and jot down key navigational landmarks.',
'Reference: BBC 6 Minute English - Listening Strategies.', 35),

-- B2 - Lesson 7
(7, 'B2', 'Grammar', 1, 'Conditionals: Second, Third, and Mixed Forms',
'Express hypothetical situations in the present (2nd Conditional) and counterfactual regrets in the past (3rd Conditional).',
'Conditionals allow speakers to speculate about possibilities, dreams, and alternate histories.\n\nSecond Conditional (Hypothetical Present/Future):\n- If + Past Simple, would + base verb.\n- "If I had more time, I would study data science."\n\nThird Conditional (Hypothetical Past / Regret):\n- If + Past Perfect (had + V3), would have + past participle.\n- "If we had left earlier, we would not have missed the flight."\n\nMixed Conditionals combine past action with present result:\n- "If he had taken the advice (past), he would be successful today (present)."',
'1. "If I were you, I would negotiate the contract terms before signing."\n2. "Had we anticipated the market shift, our revenue would have increased significantly."\n3. "If she hadn\'t studied medicine, she wouldn\'t be working as a surgeon now."',
'In formal English, "were" is preferred over "was" for all subjects in the 2nd conditional ("If I were...", "If he were...").',
'Construct a reflective statement about a past decision using the 3rd conditional.',
'Reference: Cambridge English B2 First Handbook & Advanced Grammar in Use.', 40),

-- B2 - Lesson 8
(8, 'B2', 'Writing', 2, 'Structuring Persuasive Essays & Discourse Markers',
'Organize academic paragraphs using the TEEL method (Topic, Explanation, Evidence, Link) and elevate argumentative tone.',
'A strong academic or professional essay requires coherent discourse markers. Avoid overusing simple connectors like "also" and "but". Instead, incorporate sophisticated transitions:\n- Addition: Furthermore, Moreover, In addition.\n- Contrast: Nevertheless, On the contrary, Conversely.\n- Cause & Effect: Consequently, Hence, Accordingly.\n\nStructure:\n1. Introduction with clear thesis statement.\n2. Body paragraphs with topic sentence, elaboration, and concrete evidence.\n3. Conclusion synthesizing main insights without introducing new claims.',
'1. "Furthermore, the implementation of renewable energy fosters sustainable urban economic growth."\n2. "Nevertheless, initial installation expenditures remain a formidable barrier for emerging markets."\n3. "Consequently, regulatory authorities must devise viable incentive frameworks."',
'Ensure each paragraph explores only one central argument supported by relevant illustrations.',
'Draft an opening topic sentence for an essay discussing remote workplace productivity.',
'Reference: Purdue OWL - Academic Writing & IELTS/TOEFL Writing Rubric.', 40),

-- C1 - Lesson 9
(9, 'C1', 'Grammar', 1, 'Grammatical Inversion for Emphasis & Rhetorical Impact',
'Master negative inversions, conditional inversions, and fronting constructions to craft sophisticated rhetorical prose.',
'Inversion occurs when the auxiliary verb precedes the subject, typically after negative or restrictive adverbial expressions at the beginning of a sentence. This technique adds drama, formal tone, and emphatic resonance.\n\nCommon triggers:\n- Rarely, Seldom, Little\n- Never before, Under no circumstances\n- Hardly / Scarcely ... when\n- No sooner ... than\n- Not only ... but also\n\nStructure: Negative Adverbial + Auxiliary Verb + Subject + Main Verb.',
'1. "Rarely have we witnessed such remarkable scientific collaboration across borders."\n2. "Under no circumstances should sensitive customer credentials be shared externally."\n3. "No sooner had the keynote speaker concluded her remarks than the auditorium erupted in applause."\n4. "Had the board known the financial risks, they would have declined the merger."',
'Remember that inversion requires the insertion of "do/does/did" if there is no pre-existing auxiliary verb ("Seldom did they agree on policy").',
'Rewrite standard sentences into inverted forms to heighten formal stylistic variety.',
'Reference: Cambridge English C1 Advanced - Stylistic Grammar.', 45),

-- C1 - Lesson 10
(10, 'C1', 'Vocabulary', 2, 'Collocations, Nuanced Synonyms & Academic Register',
'Distinguish subtle connotations between closely related academic verbs and deploy high-utility collocations.',
'At the C1 level, fluency shifts from mere accuracy to precise register and natural collocation. Selecting the exact nuance prevents ambiguity.\n\nExamples of fine distinctions:\n- "Mitigate" (make less severe) vs. "Exacerbate" (make worse).\n- "Corroborate" (support with evidence) vs. "Fabricate" (invent dishonestly).\n- "Pervasive" (spreading widely through an area) vs. "Ubiquitous" (present everywhere).\n\nFixed academic collocations:\n- "Mounting evidence", "Profound implications", "Cast doubt upon", "Draw a distinction".',
'1. "Recent archaeological excavations corroborate the prevailing historical theory."\n2. "Rapid urbanization has exacerbated infrastructural bottlenecks in metropolitan areas."\n3. "The committee drew a sharp distinction between intentional negligence and technical error."',
'Be cautious of false friends and check collocations using reputable corpora or learner dictionaries.',
'Incorporate three new advanced collocations into your professional communication this week.',
'Reference: Oxford Collocations Dictionary & Academic Word List (Coxhead).', 45),

-- C2 - Lesson 11
(11, 'C2', 'Reading', 1, 'Deconstructing Irony, Subtext & Stylistic Devices in Prose',
'Dissect sophisticated literary and journalistic texts, recognize subtle ironies, rhetorical questions, and implicit authorial stances.',
'Proficient C2 reading demands cognitive engagement with what is unsaid: tone, subtext, satirical undertones, and lexical ambiguity. Authors frequently employ understatement (litotes), paradox, and subtle antiphrasis to critique societal norms.\n\nTechniques to track:\n- Identifying the author\'s true stance versus ironic narrative voice.\n- Parsing multi-clause periodic sentences without losing grammatical reference.\n- Appreciating cultural and historical intertextuality.',
'Passage extract:\n"To describe the administration\'s environmental response as languid would be to pay it an unearned compliment; it was, in point of fact, a masterclass in premeditated inertia, veiled thinly behind a blizzard of non-binding communiqués."',
'Notice how the semicolon pivots the tone from restrained irony to direct indictment.',
'When reading complex essays, annotate rhetorical maneuvers in the margins.',
'Reference: Cambridge English C2 Proficiency (CPE) Reading and Use of English Guide.', 50),

-- C2 - Lesson 12
(12, 'C2', 'Speaking', 2, 'Idiomatic Mastery, High-Register Discourse & Nuance',
'Navigate diplomatic discourse, deploy rare idioms accurately, and modulate register effortlessly across demanding scenarios.',
'At the C2 proficiency benchmark, speakers possess spontaneous, highly nuanced control of English. They express shades of meaning with precision, use metaphor effortlessly, and handle colloquial idiom and elevated diplomatic rhetoric with equal dexterity.\n\nKey devices:\n- Hedging and diplomatic softening: "I would be inclined to suggest...", "It is perhaps not unreasonable to infer..."\n- Rare idioms in context: "Cut the Gordian knot" (solve a complex problem boldly), "Pyrrhic victory" (a win that inflicts devastating toll), "Throw down the gauntlet" (issue a challenge).',
'1. "Securing regulatory approval without broader stakeholder consensus would prove a Pyrrhic victory at best."\n2. "The minister adroitly hedged his assertions, ensuring that no unequivocal promises were inscribed into the record."\n3. "Faced with bureaucratic gridlock, the executive director chose to cut the Gordian knot through direct executive mandate."',
'Authentic idioms must feel organic; forcing archaic expressions without contextual justification degrades natural fluency.',
'Practice spontaneous rebuttal using diplomatic hedging techniques.',
'Reference: Oxford English Corpus & British Diplomatic Communication Seminars.', 50);

-- 4. Insert Lesson Challenges
-- A1 Challenges for Lesson 1
INSERT IGNORE INTO `challenges` (`id`, `lesson_id`, `type`, `skill`, `level`, `topic`, `difficulty`, `prompt`, `correct_answer`, `explanation`, `xp_reward`) VALUES
(1, 1, 'multiple_choice', 'Grammar', 'A1', 'Present Simple', 'easy',
'Choose the correct verb form: "My sister _______ in a hospital in Surabaya."',
'works',
'Because the subject "My sister" is third-person singular (she), we add "-s" to the base verb (works).', 10),

(2, 1, 'fill_blank', 'Grammar', 'A1', 'Present Simple', 'easy',
'Complete the sentence with the negative form of "like": They _______ spicy food.',
'do not like',
'In Present Simple with subject "They", the negative auxiliary is "do not" (or "don\'t") followed by base verb "like".', 10),

(3, 1, 'sentence_order', 'Grammar', 'A1', 'Daily Routines', 'easy',
'Arrange the words to form a correct sentence: [coffee / mornings / in / drinks / He / the]',
'["He", "drinks", "coffee", "in", "the", "mornings"]',
'Standard English sentence order is Subject (He) + Verb (drinks) + Object (coffee) + Time prepositional phrase (in the mornings).', 15);

-- A1 Challenges for Lesson 2
INSERT IGNORE INTO `challenges` (`id`, `lesson_id`, `type`, `skill`, `level`, `topic`, `difficulty`, `prompt`, `correct_answer`, `explanation`, `xp_reward`) VALUES
(4, 2, 'multiple_choice', 'Vocabulary', 'A1', 'Introductions', 'easy',
'Which article is correct? "She is _______ architect at an international firm."',
'an',
'Use "an" before words starting with a vowel sound (architect).', 10),

(5, 2, 'vocab_match', 'Vocabulary', 'A1', 'Everyday Items', 'easy',
'Match the words with their correct Indonesian meanings:',
'{"notebook":"buku catatan","keys":"kunci","bag":"tas","laptop":"komputer jinjing"}',
'Everyday objects matching: notebook = buku catatan, keys = kunci, bag = tas, laptop = komputer jinjing.', 15);

-- A2 Challenges for Lesson 3
INSERT IGNORE INTO `challenges` (`id`, `lesson_id`, `type`, `skill`, `level`, `topic`, `difficulty`, `prompt`, `correct_answer`, `explanation`, `xp_reward`) VALUES
(6, 3, 'multiple_choice', 'Grammar', 'A2', 'Past Simple', 'easy',
'Identify the correct sentence in the Past Simple tense:',
'She wrote an informative letter yesterday.',
'"She wrote" uses the irregular past simple of write. Auxiliary "did" must not be followed by past tense ("did not wrote" is incorrect).', 10),

(7, 3, 'grammar_correction', 'Grammar', 'A2', 'Past Simple', 'medium',
'Correct the grammatical error in this sentence: "We didn\'t saw him at the conference yesterday."',
'We didn\'t see him at the conference yesterday.',
'After the negative auxiliary "didn\'t", the main verb must remain in its base form (see, not saw).', 15);

-- A2 Challenges for Lesson 4
INSERT IGNORE INTO `challenges` (`id`, `lesson_id`, `type`, `skill`, `level`, `topic`, `difficulty`, `prompt`, `passage`, `correct_answer`, `explanation`, `xp_reward`) VALUES
(8, 4, 'reading_comprehension', 'Reading', 'A2', 'Chronology', 'easy',
'What did Maya do immediately after meeting her supervisor?',
'Maya registered for the morning seminar first. Then she met with her faculty supervisor. After that, they reviewed the laboratory schedule together. Finally, she submitted her completed syllabus at noon.',
'She reviewed the laboratory schedule.',
'According to the chronological sequence: "After that, they reviewed the laboratory schedule together."', 15);

-- B1 Challenges for Lesson 5 & 6
INSERT IGNORE INTO `challenges` (`id`, `lesson_id`, `type`, `skill`, `level`, `topic`, `difficulty`, `prompt`, `correct_answer`, `explanation`, `xp_reward`) VALUES
(9, 5, 'multiple_choice', 'Grammar', 'B1', 'Present Perfect', 'medium',
'Choose the correct option: "I _______ lived in Bandung since 2018."',
'have',
'The Present Perfect formula is Subject (I) + have + past participle (lived).', 10),

(10, 6, 'listening_comprehension', 'Listening', 'B1', 'Workplace Directions', 'medium',
'Listen to the audio direction. Where is the supervisor\'s office located?',
'Directly opposite the cafeteria on the second floor.',
'The speaker confirms the supervisor\'s office is situated directly opposite the cafeteria.', 15);

-- B2 Challenges for Lesson 7 & 8
INSERT IGNORE INTO `challenges` (`id`, `lesson_id`, `type`, `skill`, `level`, `topic`, `difficulty`, `prompt`, `correct_answer`, `explanation`, `xp_reward`) VALUES
(11, 7, 'multiple_choice', 'Grammar', 'B2', 'Conditionals', 'medium',
'Select the grammatically sound 2nd conditional: "If I _______ more free time, I would learn Arabic."',
'had',
'In the second conditional, the "if" clause takes the Past Simple (had) to denote a hypothetical present condition.', 15),

(12, 8, 'short_answer', 'Writing', 'B2', 'Discourse Markers', 'medium',
'Supply a formal single-word transition indicating cause and effect equivalent to "as a result" or "therefore":',
'Consequently',
'"Consequently" (or "Hence" / "Accordingly") is the standard formal transition denoting logical result in academic essays.', 15);

-- C1 Challenges for Lesson 9 & 10
INSERT IGNORE INTO `challenges` (`id`, `lesson_id`, `type`, `skill`, `level`, `topic`, `difficulty`, `prompt`, `correct_answer`, `explanation`, `xp_reward`) VALUES
(13, 9, 'multiple_choice', 'Grammar', 'C1', 'Inversion', 'hard',
'Choose the correct inverted sentence:',
'Rarely have we encountered such profound analytical rigor.',
'After restrictive negative adverbials like "Rarely", inversion requires auxiliary (have) + subject (we) + past participle (encountered).', 20),

(14, 10, 'multiple_choice', 'Vocabulary', 'C1', 'Academic Collocations', 'hard',
'Select the verb that accurately collocates with "evidence" to mean "support or confirm":',
'corroborate',
'To "corroborate evidence" means to confirm or give support to a statement, theory, or finding.', 20);

-- C2 Challenges for Lesson 11 & 12
INSERT IGNORE INTO `challenges` (`id`, `lesson_id`, `type`, `skill`, `level`, `topic`, `difficulty`, `prompt`, `passage`, `correct_answer`, `explanation`, `xp_reward`) VALUES
(15, 11, 'reading_comprehension', 'Reading', 'C2', 'Literary Stylistics', 'hard',
'What is the author\'s true opinion regarding the administration\'s environmental response?',
'To describe the administration\'s environmental response as languid would be to pay it an unearned compliment; it was, in point of fact, a masterclass in premeditated inertia, veiled thinly behind a blizzard of non-binding communiqués.',
'It was deliberately inactive and thoroughly ineffective.',
'The phrase "a masterclass in premeditated inertia" demonstrates deliberate, calculated inaction rather than mere slow action.', 25),

(16, 12, 'multiple_choice', 'Speaking', 'C2', 'Idiomatic Nuance', 'hard',
'What does it mean to achieve a "Pyrrhic victory"?',
NULL,
'A victory won at such a devastating cost that it is tantamount to defeat.',
'A Pyrrhic victory originates from King Pyrrhus of Epirus, whose triumph against the Romans incurred irreplaceable losses.',
25);

-- 5. Insert Challenge Options for Multiple Choice Challenges
INSERT IGNORE INTO `challenge_options` (`id`, `challenge_id`, `option_key`, `option_text`, `is_correct`) VALUES
-- Options for Challenge 1 (A1)
(1, 1, 'A', 'work', 0),
(2, 1, 'B', 'works', 1),
(3, 1, 'C', 'working', 0),
(4, 1, 'D', 'worked', 0),

-- Options for Challenge 4 (A1)
(5, 4, 'A', 'a', 0),
(6, 4, 'B', 'an', 1),
(7, 4, 'C', 'the', 0),
(8, 4, 'D', 'some', 0),

-- Options for Challenge 6 (A2)
(9, 6, 'A', 'She wrote an informative letter yesterday.', 1),
(10, 6, 'B', 'She writed an informative letter yesterday.', 0),
(11, 6, 'C', 'She did not wrote an informative letter yesterday.', 0),
(12, 6, 'D', 'She was write an informative letter yesterday.', 0),

-- Options for Challenge 8 (A2 Reading)
(13, 8, 'A', 'She met her faculty supervisor.', 0),
(14, 8, 'B', 'She reviewed the laboratory schedule.', 1),
(15, 8, 'C', 'She registered for the morning seminar.', 0),
(16, 8, 'D', 'She went home immediately.', 0),

-- Options for Challenge 9 (B1)
(17, 9, 'A', 'has', 0),
(18, 9, 'B', 'have', 1),
(19, 9, 'C', 'had', 0),
(20, 9, 'D', 'am', 0),

-- Options for Challenge 10 (B1 Listening)
(21, 10, 'A', 'Directly opposite the cafeteria on the second floor.', 1),
(22, 10, 'B', 'Inside the conference hall on the first floor.', 0),
(23, 10, 'C', 'Near the main entrance garden.', 0),
(24, 10, 'D', 'Beside the security checkpoint.', 0),

-- Options for Challenge 11 (B2)
(25, 11, 'A', 'have', 0),
(26, 11, 'B', 'had', 1),
(27, 11, 'C', 'will have', 0),
(28, 11, 'D', 'would have', 0),

-- Options for Challenge 13 (C1)
(29, 13, 'A', 'Rarely we have encountered such profound analytical rigor.', 0),
(30, 13, 'B', 'Rarely have we encountered such profound analytical rigor.', 1),
(31, 13, 'C', 'Rarely did we encountered such profound analytical rigor.', 0),
(32, 13, 'D', 'Rarely had we encounter such profound analytical rigor.', 0),

-- Options for Challenge 14 (C1)
(33, 14, 'A', 'fabricate', 0),
(34, 14, 'B', 'corroborate', 1),
(35, 14, 'C', 'exacerbate', 0),
(36, 14, 'D', 'alleviate', 0),

-- Options for Challenge 15 (C2 Reading)
(37, 15, 'A', 'It was energetic and highly praised by the public.', 0),
(38, 15, 'B', 'It was deliberately inactive and thoroughly ineffective.', 1),
(39, 15, 'C', 'It produced legally binding landmark accords.', 0),
(40, 15, 'D', 'It was unintentionally slow due to logistical hurdles.', 0),

-- Options for Challenge 16 (C2)
(41, 16, 'A', 'A triumph achieved with total ease and minimal effort.', 0),
(42, 16, 'B', 'A victory won at such a devastating cost that it is tantamount to defeat.', 1),
(43, 16, 'C', 'An unexpected victory achieved through cunning deception.', 0),
(44, 16, 'D', 'A draw disguised as a diplomatic triumph.', 0);

-- Update audio_text for listening challenge
UPDATE `challenges` SET `audio_text` = 'Excuse me, could you direct me to the supervisor''s office? Certainly! Walk straight past the reception desk, take the stairs to the second floor, and it is situated directly opposite the cafeteria.' WHERE `id` = 10;

-- 6. Insert Dedicated Academy & Exam Practice Questions (Standalone Challenges)
INSERT IGNORE INTO `challenges` (`id`, `lesson_id`, `type`, `skill`, `level`, `topic`, `difficulty`, `prompt`, `passage`, `audio_text`, `correct_answer`, `explanation`, `xp_reward`) VALUES
-- Academy Grammar Gym
(101, NULL, 'multiple_choice', 'Grammar', 'B1', 'Modals', 'medium',
'Choose the best modal verb: "You _______ submit your application before 5:00 PM; late submissions will be rejected."', NULL, NULL,
'must', 'Must indicates a firm obligation or necessity imposed by rules.', 15),

(102, NULL, 'multiple_choice', 'Grammar', 'B2', 'Passive Voice', 'medium',
'Convert to passive: "The engineers are testing the new aerodynamic prototype."', NULL, NULL,
'The new aerodynamic prototype is being tested by the engineers.', 'Present continuous passive: Subject + is/are + being + past participle (tested).', 15),

-- Academy Vocabulary Practice
(103, NULL, 'multiple_choice', 'Vocabulary', 'B2', 'Business & Tech', 'medium',
'Select the word meaning "the ability to recover quickly from difficulties":', NULL, NULL,
'resilience', 'Resilience is psychological strength and the capacity to rebound from adversity.', 15),

(104, NULL, 'multiple_choice', 'Vocabulary', 'C1', 'Academic Verbs', 'hard',
'Which word means "to make an unpleasant feeling less intense"?', NULL, NULL,
'assuage', 'To assuage means to soothe, pacify, or alleviate distress.', 20),

-- Academy Reading Practice
(105, NULL, 'multiple_choice', 'Reading', 'B1', 'Science & Environment', 'medium',
'According to the passage, why are mangrove forests critical to coastal ecosystems?',
'Mangrove ecosystems serve as natural buffers against coastal erosion and storm surges. Their dense aerial root systems trap sediment, stabilizing shorelines while sequestering carbon at rates significantly higher than terrestrial tropical rainforests. Furthermore, they provide essential nurseries for juvenile marine species.',
NULL,
'They act as coastal buffers and sequester substantial carbon.',
'The text explicitly highlights their role as storm buffers, shoreline stabilizers, and superior carbon sinks.', 15),

-- Academy Listening Practice
(106, NULL, 'multiple_choice', 'Listening', 'B2', 'Academic Lecture', 'medium',
'Listen to the lecture excerpt. What main challenge regarding renewable microgrids does the speaker emphasize?',
NULL,
'While decentralized solar microgrids offer unprecedented access to remote communities, intermittent sunlight necessitates substantial battery storage capacity, which currently constitutes the most cost-intensive component of installation.',
'The high financial cost of battery storage systems.',
'The speaker stresses that battery storage currently constitutes the most cost-intensive component.', 20),

-- Exam Lab TOEFL Practice (Section 1: Structure & Written Expression)
(201, NULL, 'multiple_choice', 'Grammar', 'B2', 'TOEFL Structure', 'medium',
'_______ extensive geological surveys, the exploratory drilling project discovered substantial geothermal reserves.', NULL, NULL,
'Following', 'Preposition "Following" introduces the noun phrase complement without creating a comma splice.', 15),

(202, NULL, 'multiple_choice', 'Grammar', 'C1', 'TOEFL Structure', 'hard',
'Not until the invention of the electron microscope _______ to observe viral structures with high resolution.', NULL, NULL,
'were scientists able', 'Negative introductory time phrase "Not until..." triggers subject-auxiliary inversion (were scientists able).', 20),

-- Exam Lab TOEFL Practice (Section 2: Reading Comprehension)
(203, NULL, 'multiple_choice', 'Reading', 'B2', 'TOEFL Reading', 'medium',
'The passage suggests that early bioluminescent signaling in deep-sea organisms primarily evolved to:',
'In the aphotic oceanic abyss, sunlight fails to penetrate beyond two hundred meters. Organisms inhabiting this bathypelagic zone have evolved sophisticated bioluminescent photophores. Rather than serving purely predatory objectives, evidence indicates that rudimentary luminescence originally functioned as a metabolic byproduct, only subsequently being co-opted for conspecific communication and counterillumination camouflage.',
NULL,
'Dissipate metabolic byproducts before adapting for communication.',
'The passage states that luminescence originally functioned as a metabolic byproduct before evolutionary adaptation for communication.', 20),

-- Exam Lab TOEFL Practice (Section 3: Listening Comprehension)
(204, NULL, 'multiple_choice', 'Listening', 'B2', 'TOEFL Listening', 'medium',
'What is the student\'s main inquiry regarding the term paper citation policy?',
NULL,
'Professor Vance, I was reviewing the syllabus regarding the literature review. Are we permitted to cite preprint repositories like arXiv, or are we strictly confined to peer-reviewed academic journals for our primary bibliographies?',
'Whether non-peer-reviewed preprints are acceptable sources.',
'The student explicitly asks if preprint repositories like arXiv are permitted alongside peer-reviewed journals.', 20);

-- Options for Academy & Exam Questions
INSERT IGNORE INTO `challenge_options` (`id`, `challenge_id`, `option_key`, `option_text`, `is_correct`) VALUES
-- 101 Modals
(1011, 101, 'A', 'might', 0),
(1012, 101, 'B', 'must', 1),
(1013, 101, 'C', 'could', 0),
(1014, 101, 'D', 'shall', 0),

-- 102 Passive
(1021, 102, 'A', 'The new aerodynamic prototype is being tested by the engineers.', 1),
(1022, 102, 'B', 'The new aerodynamic prototype was tested by the engineers.', 0),
(1023, 102, 'C', 'The new aerodynamic prototype has been testing by the engineers.', 0),
(1024, 102, 'D', 'The engineers were tested by the new aerodynamic prototype.', 0),

-- 103 Resilience
(1031, 103, 'A', 'reluctance', 0),
(1032, 103, 'B', 'resilience', 1),
(1033, 103, 'C', 'reticence', 0),
(1034, 103, 'D', 'redundancy', 0),

-- 104 Assuage
(1041, 104, 'A', 'assuage', 1),
(1042, 104, 'B', 'provoke', 0),
(1043, 104, 'C', 'escalate', 0),
(1044, 104, 'D', 'repudiate', 0),

-- 105 Mangrove
(1051, 105, 'A', 'They act as coastal buffers and sequester substantial carbon.', 1),
(1052, 105, 'B', 'They increase salinity and impede marine navigation.', 0),
(1053, 105, 'C', 'They replace coral reefs in temperate latitudes.', 0),
(1054, 105, 'D', 'They are primarily cultivated for industrial timber.', 0),

-- 106 Microgrids
(1061, 106, 'A', 'The lack of public interest in clean technology.', 0),
(1062, 106, 'B', 'The high financial cost of battery storage systems.', 1),
(1063, 106, 'C', 'Government regulations prohibiting solar panels.', 0),
(1064, 106, 'D', 'The absence of solar radiation in tropical zones.', 0),

-- 201 TOEFL Structure 1
(2011, 201, 'A', 'Because', 0),
(2012, 201, 'B', 'Following', 1),
(2013, 201, 'C', 'Although', 0),
(2014, 201, 'D', 'In order to', 0),

-- 202 TOEFL Structure 2
(2021, 202, 'A', 'scientists were able', 0),
(2022, 202, 'B', 'were scientists able', 1),
(2023, 202, 'C', 'scientists could have', 0),
(2024, 202, 'D', 'did scientists able', 0),

-- 203 TOEFL Reading
(2031, 203, 'A', 'Dissipate metabolic byproducts before adapting for communication.', 1),
(2032, 203, 'B', 'Illuminate benthic trenches to locate prey items.', 0),
(2033, 203, 'C', 'Ward off apex cetacean predators during migration.', 0),
(2034, 203, 'D', 'Attract photosynthetic symbionts in sunlight.', 0),

-- 204 TOEFL Listening
(2041, 204, 'A', 'Whether non-peer-reviewed preprints are acceptable sources.', 1),
(2042, 204, 'B', 'How to extend the final submission deadline.', 0),
(2043, 204, 'C', 'The formatting standards for APA style bibliographies.', 0),
(2044, 204, 'D', 'Which textbook is required for the mid-term examination.', 0);

-- 7. Seed Real XP Transactions for Initial Leaderboard (with timestamps in the current week!)
INSERT IGNORE INTO `xp_transactions` (`user_id`, `amount`, `source_type`, `source_id`, `event_key`, `description`, `created_at`) VALUES
-- Budi Santoso (Active this week and earlier)
(1, 25, 'lesson_completion', 1, 'lesson_1_u1', 'Completed Lesson: Present Simple', NOW() - INTERVAL 6 DAY),
(1, 10, 'challenge_completion', 1, 'challenge_1_u1', 'Completed Challenge #1', NOW() - INTERVAL 6 DAY),
(1, 25, 'lesson_completion', 2, 'lesson_2_u1', 'Completed Lesson: Everyday Objects', NOW() - INTERVAL 5 DAY),
(1, 30, 'lesson_completion', 3, 'lesson_3_u1', 'Completed Lesson: Past Simple', NOW() - INTERVAL 3 DAY),
(1, 50, 'achievement', 1, 'ach_1_u1', 'Unlocked Achievement: Langkah Pertama', NOW() - INTERVAL 6 DAY),
(1, 40, 'academy_practice', NULL, 'acad_1_u1', 'Completed Academy Grammar Practice', NOW() - INTERVAL 1 DAY),
(1, 60, 'exam_completion', NULL, 'exam_1_u1', 'Completed TOEFL Objective Practice Test', NOW() - INTERVAL 2 HOUR),

-- Siti Aminah
(2, 25, 'lesson_completion', 1, 'lesson_1_u2', 'Completed Lesson: Present Simple', NOW() - INTERVAL 4 DAY),
(2, 10, 'challenge_completion', 1, 'challenge_1_u2', 'Completed Challenge #1', NOW() - INTERVAL 4 DAY),
(2, 35, 'lesson_completion', 5, 'lesson_5_u2', 'Completed Lesson: Present Perfect', NOW() - INTERVAL 2 DAY),
(2, 50, 'achievement', 1, 'ach_1_u2', 'Unlocked Achievement: Langkah Pertama', NOW() - INTERVAL 4 DAY),
(2, 45, 'academy_practice', NULL, 'acad_1_u2', 'Completed Academy Vocab Practice', NOW() - INTERVAL 1 DAY),

-- Kevin Wijaya
(3, 25, 'lesson_completion', 1, 'lesson_1_u3', 'Completed Lesson: Present Simple', NOW() - INTERVAL 3 DAY),
(3, 40, 'lesson_completion', 7, 'lesson_7_u3', 'Completed Lesson: Conditionals', NOW() - INTERVAL 1 DAY),
(3, 50, 'achievement', 1, 'ach_1_u3', 'Unlocked Achievement: Langkah Pertama', NOW() - INTERVAL 3 DAY),

-- Dian Lestari
(4, 25, 'lesson_completion', 1, 'lesson_1_u4', 'Completed Lesson: Present Simple', NOW() - INTERVAL 1 DAY),
(4, 50, 'achievement', 1, 'ach_1_u4', 'Unlocked Achievement: Langkah Pertama', NOW() - INTERVAL 1 DAY);

-- 8. Seed Lesson Progress
INSERT IGNORE INTO `user_lesson_progress` (`user_id`, `lesson_id`, `is_completed`, `completed_at`) VALUES
(1, 1, 1, NOW() - INTERVAL 6 DAY),
(1, 2, 1, NOW() - INTERVAL 5 DAY),
(1, 3, 1, NOW() - INTERVAL 3 DAY),
(2, 1, 1, NOW() - INTERVAL 4 DAY),
(2, 5, 1, NOW() - INTERVAL 2 DAY),
(3, 1, 1, NOW() - INTERVAL 3 DAY),
(3, 7, 1, NOW() - INTERVAL 1 DAY),
(4, 1, 1, NOW() - INTERVAL 1 DAY);

-- 9. Seed User Achievements
INSERT IGNORE INTO `user_achievements` (`user_id`, `achievement_id`, `unlocked_at`) VALUES
(1, 1, NOW() - INTERVAL 6 DAY),
(1, 2, NOW() - INTERVAL 6 DAY),
(2, 1, NOW() - INTERVAL 4 DAY),
(3, 1, NOW() - INTERVAL 3 DAY),
(4, 1, NOW() - INTERVAL 1 DAY);

-- 10. Seed Exam Attempts
INSERT IGNORE INTO `exam_attempts` (`user_id`, `exam_title`, `reading_score`, `listening_score`, `structure_score`, `total_score`, `max_score`, `duration_seconds`, `completed_at`) VALUES
(1, 'TOEFL Practice Test - Complete Simulation 1', 20, 20, 35, 75, 100, 1140, NOW() - INTERVAL 2 HOUR);
